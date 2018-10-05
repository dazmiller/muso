import AbstractController from '../../common/controllers/abstract.controller';

class UsersController extends AbstractController{
  constructor() {
    super(UsersController.$inject, arguments);

    this.genders = ['Male','Female','Other'];
    this.$scope.$watch('usersCtr.user.image', (newValue, oldValue) => {
      if(newValue && newValue instanceof File){
        this.showImage(newValue);
      }
    });
  }

  index(params){
    this.searching  = false;

    this.Paginator.start();
    this.loadUsers();
  }

  show(params){
    this.User.show(params.id)
      .then(this.setUser.bind(this));
  }

  save(user){
    this.User.update(user)
      .then(this.setUser.bind(this));
  }

  loadUsers(){
    var params = {
      page: this.Paginator.getPage()
    };

    if(this.searching){
      params.search = this.query;
    }

    if(this.$state.current.name === 'admin.users.authors'){
      params.authors = true;
    }

    this.User.latest(params)
      .then(({ meta, users }) => {
        if(this.Paginator.getPage() > 0 && this.users){
          this.users = [...this.users, ...users];
        }else{
          this.users = users;
        }

        this.total = meta.total;
        this.current = this.users.length;
      });
  }

  setUser({ user }) {
    if(user.dob) {
      user.dob = new Date(user.dob);
    }
    user.admin = !!user.admin;
    user.author = !!user.author;
    user.avatar = user.image;

    this.user = user;
  }

  nextPage(page){
    this.loadUsers();
  }

  search(){
    this.Paginator.start();
    this.loadUsers();
  }

  toggleSearch(){
    this.searching = !this.searching;
    
    //if closing the search form
    //load all users
    if(!this.searching){
      this.Paginator.start();
      this.query = '';
      this.loadUsers();
    }
  }

  showImage(file){
    let reader = new FileReader();

    reader.onload = (event) => {
      let img = document.getElementById('user-image');
      img.src = event.target.result;
    }
    reader.readAsDataURL(file);
  }

  deleteUser(user, event) {
    event.preventDefault();
    event.stopPropagation();

    var confirm = this.$mdDialog.confirm()
      .title(this.$translate.instant('ARE_YOU_SURE'))
      .textContent(this.$translate.instant(user.author ? 'USERS_DELETE_AUTHOR_CONTENT' : 'USERS_DELETE_CONTENT'))
      .ariaLabel('Delete user')
      .targetEvent(event)
      .ok(this.$translate.instant('YES'))
      .cancel(this.$translate.instant('NO'));

    this.$mdDialog.show(confirm)
      .then(() => {
        this.User.remove(user)
          .then(() => {
            this.users = this.users.filter(u => u.id !== user.id);
          });
      });
  }
}

UsersController.$inject = ['$state', '$scope', '$mdDialog', '$translate','User', 'Paginator'];

export default UsersController;