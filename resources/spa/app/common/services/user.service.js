import { toApiDate } from '../utils/date';

const User = ($auth, $q, MailBox, Connection)=>{
  let current;
  const service = Connection.resource('/users');
  const artistService = Connection.resource('/artists');

  service.isSessionValid = () => {
    return $auth.isAuthenticated();
  }

  service.requestCurrentUser = () => {
    let promise;

    //cache the current user to only ask once
    if (current) {
      return Promise.resolve(current);
    } else {
      promise = service.show('current')
        .then(({ user }) => {
          service.setCurrent(user);

          return Promise.resolve(user);
        })
        .catch(error => {
          $auth.logout();
        });
    }

    return promise;
  }


  service.setCurrent = (user) => {
    current = user;
    Connection.setToken($auth.getToken());
  };

  service.updatePasswd = (user) => {
    return Connection.post({
      url: `/users/${user.id}`,
      data: {
        name: user.name,
        password: user.password,
      },
    });
  };

  service.update = (user) => {
    const form = {
      id: user.id,
      name: user.name,
      email: user.email,
      about: user.about,
      occupation: user.occupation,
      country: user.country,
      website: user.website,
      postcode: user.postcode,
      gender: user.gender,
      author: user.author,
      admin: user.admin,
    };

    if(user.image instanceof File){
      form.image = user.image;
    }

    if (user.dob) {
      form.dob = toApiDate(user.dob);
    }

    return Connection.post({
        url: `/users/${user.id}`,
        form,
      })
      .then((response) => {
        service.clearCache();

        return Promise.resolve(response);
      });
  }

  service.getPublishedAlbums = (userId) => {
    return artistService.show(`${userId}/albums`);
  }

  service.getAuthors = (data) => {
    return artistService.all(data);
  }

  service.forgotPassword = (user) => {
    return Connection.post({
      url: '/auth/forgot',
      data: user,
    });
  }

  service.recoverPassword = (token) => {
    return Connection.get({
      url: `/auth/recover/${token}`,
    });
  }

  service.latest = (params) => {
    return service.all(params);
  }

  service.logout = () => {
    current = null;
    $auth.logout();
    Connection.setToken(null);

    // Clear cache for each service related to the user
    MailBox.clearCache();
  }

  service.byId = (id) => {
    return service.show(id);
  }

  service.searchAuthors = (query) => {
    return Connection.get({
      url: '/search/artists',
      data: { query }
    });
  };

  service.followers = (id => Connection.get({
    url: `/users/${id}/followers`,
  }));

  service.followings = (id => Connection.get({
    url: `/users/${id}/followings`,
  }));

  service.follow = (id => Connection.get({
    url: `/users/${id}/follow`,
  }));
  
  service.unfollow = (id => Connection.get({
    url: `/users/${id}/unfollow`,
  }));
  
  service.feed = ((id, page = 0) => Connection.get({
    url: `/users/${id}/feed?page=${page}`,
  }));
  
  service.countries = ((data) => Connection.get({
    url: '/users/countries',
    data,
  }));

  return service;
}

User.$inject = ['$auth', '$q', 'MailBox', 'Connection'];

export default User;
