
class AuthorsController {
  constructor(User, Paginator) {
    this.User = User;
    this.Paginator = Paginator;
    this.country = 'all';
    this.gender = 'any';

    this.Paginator.reset();
    this.loadAuthors();
    this.User.countries({ authors: true })
      .then(({ countries }) => {
        this.countries = countries;
      });
  }

  loadAuthors() {
    let params = {
      page: this.Paginator.getPage(),
      authors: true,
    };

    if (this.gender && this.gender !== 'any') {
      params.gender = this.gender;
    }

    if (this.country && this.country !== 'all') {
      params.country = this.country;
    }

    this.User.latest(params)
      .then(({ users }) => {
        
        if (this.Paginator.getPage() > 1 && users) {
          this.latests = [...this.latests, ...users];
        } else {
          this.latests = users;
        }
      });
  }

  setGender(gender) {
    if (gender !== this.gender) {
      this.gender = gender;
      this.Paginator.reset();
      this.loadAuthors();
    }
  }
  
  setCountry(country) {
    if (country !== this.country) {
      this.country = country;
      this.Paginator.reset();
      this.loadAuthors();
    }
  }
}

AuthorsController.$inject = ['User', 'Paginator'];

export default AuthorsController;