import AbstractController from '../../common/controllers/abstract.controller';

class AlbumsController extends AbstractController{
    constructor(){
        super(AlbumsController.$inject, arguments);
    }

    index(params){
        this.Paginator.start();
        this.searching  = false;

        this.loadAlbums();
    }

    loadAlbums(){
        var params = {
            page    : this.Paginator.getPage(),
            others  : true
        };

        if(this.searching){
            params.search = this.query;
        }

        if(this.$state.current.name === 'admin.albums.published'){
            params.published = true;
        }
        if(this.$state.current.name === 'admin.albums.unpublished'){
            params.published = false;
        }

        this.Album.all(params)
            .then(({ albums, meta })=>{
                if(this.Paginator.getPage() > 0 && this.albums){
                    this.albums = [...this.albums, ...albums];
                }else{
                    this.albums = albums;
                }

                this.total  = meta.total;
                this.current= this.albums.length;
            });
    }

    nextPage(page){
        this.loadAlbums();
    }

    search(){
        this.Paginator.start();
        this.loadAlbums();
    }

    toggleSearch(){
        this.searching = !this.searching;
        
        //if closing the search form
        //load all users
        if(!this.searching){
            this.Paginator.start();
            this.query = '';
            this.loadAlbums();
        }
    }

    deleteAlbum(album, event) {
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

AlbumsController.$inject = ['$state', '$scope', '$mdDialog', '$translate', 'Album', 'Paginator'];

export default AlbumsController;