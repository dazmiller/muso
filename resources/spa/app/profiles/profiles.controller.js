import AbstractController from '../common/controllers/abstract.controller';

class ProfilesController extends AbstractController{
  constructor(){
    super(ProfilesController.$inject,arguments);

    this.$scope.$watch('profiles.user.image', (newValue, oldValue) => {
      if(newValue && newValue instanceof File){
        this.showImage(newValue);
      }
    });
  }

  index(params) {
    this.userId = params.id;
    this.page = 0;
    this.selected = 0;
    this.totalFollowers = 0;
    this.totalFollowings = 0;
    this.genders = ['Male','Female','Other'];
    this.activities = [];
    this.followers = [];
    this.followings = [];
    this.showLoadMore = true;

    this.User.requestCurrentUser()
      .then((user) => {
        this.currentUser = user;
      });

    this.User.show(this.userId)
      .then(({ user })=>{
        if(user.dob){
          user.dob = new Date(user.dob);
        }
        this.user = user;

        this.User.getPublishedAlbums(user.id)
          .then((response) => {
            const songs = [];
            response.albums.forEach(
              album => album.songs.forEach(
                song => songs.push({
                  ...song,
                  album: album,
                  author: user,
                })
              )
            );

            this.songs = songs;
            if (this.$state.current.name === 'members.profiles.settings') {
              this.selected = 3;
            }
          });
      });
    
    this.User.feed(this.userId, this.page)
      .then(({ activities }) => {
        this.activities = activities;
      });

    this.User.followers(this.userId)
      .then(({ followers, meta }) => {
        this.followers = followers;
        this.totalFollowers = meta.total;
        this.amIFollowing = meta.following;
      });
    
    this.User.followings(this.userId)
      .then(({ followers, meta }) => {
        this.followings = followers;
        this.totalFollowings = meta.total;
      });
  }

  save(user){
    if (user.email && user.name) {
      this.User.update(user);
    }
  }

  savePasswd(user) {
    this.User.updatePasswd(user);
  }

  showImage(file){
    let reader = new FileReader();

    reader.onload = (event) => {
      let img = document.getElementById('user-image');
      img.src = event.target.result;
    }
    reader.readAsDataURL(file);

    this.User.update({
      id    : this.user.id,
      name  : this.user.name,
      email  : this.user.email,
      image : file
    }).then(({ user }) => {
      this.user.image = user.image;
    });
  }

  contact(user) {
    this.$state.go('members.mailbox.compose', { userId: user.id });
  }

  follow(user) {
    this.User.follow(user.id)
      .then(() => {
        this.amIFollowing = true;
        this.totalFollowers = this.totalFollowers + 1;
        this.followers = [
          {
            ...this.currentUser,
            time: { date: Date.now() },
          },
          ...this.followers,
        ];
      });
  }
  
  unfollow(user) {
    this.User.unfollow(user.id)
      .then(() => {
        this.amIFollowing = false;
        this.totalFollowers = this.totalFollowers - 1;
      });
  }

  loadNextPage() {
    this.page = this.page + 1;

    this.User.feed(this.userId, this.page)
      .then(({ activities }) => {
        this.activities = [...this.activities, ...activities];

        if (activities.length === 0) {
          this.showLoadMore = false;
        }
      });
  }
}

ProfilesController.$inject = ['$state','$scope','User'];

export default ProfilesController;