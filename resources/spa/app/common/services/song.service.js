
const Song = ($state, Connection) => {
  const service = Connection.resource('/songs');
  const discoverGenreService = Connection.resource('/discovers/genre');
  const discoverTagService = Connection.resource('/discovers/tag');

  service.like = (song) => {
    return service.show(`${song.id}/likeable`)
      .then((response) => {
        service.clearCache();
        return Promise.resolve(response);
      })
      .catch((response) => {
        if (response.error === 'token_not_provided') {
          $state.go('public.auth');
        }
      });
  };

  service.getByGenre = (genre, data) => {
    return discoverGenreService.show(genre, data);
  };

  service.getByTag = (tag, params) => {
    return discoverTagService.show(tag, params);
  };

  service.getUsersWhoLikedSong = (song) => {
    return Connection.get({
      url: `/discovers/users/${song.id}/liked`,
    });
  };

  service.search = (query) => {
    return Connection.get({
      url: '/search/songs',
      data: { query }
    });
  };

  service.download = (song) => {
    return Connection.get({
      url: `/songs/${song.id}/download`,
    });
  };

  return service;
}

Song.$inject = ['$state', 'Connection'];

export default Song;