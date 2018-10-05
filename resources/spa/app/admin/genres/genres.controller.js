import AbstractController from '../../common/controllers/abstract.controller';

class AdminGenresController extends AbstractController{
    constructor(){
        super(AdminGenresController.$inject, arguments);
    }

    index(params){
        this.loadGenres();
    }

    loadGenres(){
        this.Genre.all()
            .then((response) => {
                this.genres = response.genres;
                this.total = response.meta.total;
            });
    }

    save(genre){
        if(genre.id){
            this.Genre.update(genre)
                .then(()=>{
                    this.toggleForm('close');
                });
        }else{
            this.Genre.store(genre)
                .then((response)=>{
                    this.genres.unshift(response.genre);
                    this.toggleForm('close');
                });
        }
    }

    toggleForm(genre = {}, event = {}) {
        event.cancelBubble = true;

        if(genre !== 'close'){
            this.genre = genre;
        }

        this.$mdSidenav('genre-form').toggle();
    }

    deleteGenre(genre, event){
        event.cancelBubble = true;

        var confirm = this.$mdDialog.confirm()
                  .title(this.$translate.instant('ARE_YOU_SURE'))
                  .textContent(this.$translate.instant('GENRES_DELETE_WARNING'))
                  .ariaLabel('Delete genre')
                  .targetEvent(event)
                  .ok(this.$translate.instant('YES'))
                  .cancel(this.$translate.instant('NO'));

        this.$mdDialog.show(confirm)
            .then(() => {
                this.Genre.remove(genre)
                    .then(() => {
                        this.genres = this.genres.filter(gre => gre.id !== genre.id);
                    });
            });
    }
}

AdminGenresController.$inject = ['$scope', '$state','$mdSidenav','$mdDialog','$translate','Genre'];

export default AdminGenresController;