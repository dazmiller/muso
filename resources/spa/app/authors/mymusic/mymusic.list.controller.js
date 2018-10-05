import AbstractController from '../../common/controllers/abstract.controller';


class ListController extends AbstractController{

    constructor(){
        super(ListController.$inject, arguments);
    }

    index(){
        const params = {};

        if (this.$state.current.name === 'authors.mymusic.published') {
            params.published = true;
        }
        
        if (this.$state.current.name === 'authors.mymusic.unpublished') {
            params.published = false;
        }

        this.Album.all(params)
            .then(({ albums }) => {
                this.albums = albums;
            });
        
    }

    deleteAlbum(album, event){
        event.preventDefault();
        event.stopPropagation();
        
        var confirm = this.$mdDialog.confirm()
                  .title(this.$translate.instant('ARE_YOU_SURE'))
                  .textContent(this.$translate.instant('ALBUM_DELETE_CONTENT'))
                  .ariaLabel('Delete albumn')
                  .targetEvent(event)
                  .ok(this.$translate.instant('YES'))
                  .cancel(this.$translate.instant('NO'));

        this.$mdDialog.show(confirm)
            .then(() => {
                this.Album.remove(album)
                    .then(() => {
                        this.albums = this.albums.filter(albm => albm.id !== album.id);
                    });
            });
    }
}

//IMPORTANT: $state service should always be injected,
//its required to execute the correct method
ListController.$inject = ['$state', '$mdDialog', '$translate', 'Album'];

export default ListController;