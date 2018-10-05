
class FavoritesController{
    constructor($rootScope,$auth,Song,User,Playlist,Paginator){

        if($auth.isAuthenticated()){
            User.requestCurrentUser()
                .then((data)=>{
                    this.currentUser = data;
                });
        }

        this.$rootScope = $rootScope;
        this.Song = Song;
        this.Paginator = Paginator;
        this.Playlist = Playlist;

        Paginator.start();
        this.loadFavorites();
    }

    loadFavorites(){

        this.Playlist.getFavorites({page:this.Paginator.getPage()})
            .then(({ favorites, meta })=>{

                if(this.Paginator.getPage() > 0 && this.favorites){
                    this.favorites.push(...favorites);
                }else{
                    this.favorites = favorites;
                }

                this.total  = meta.total;
                this.current= this.favorites.length;
            });
    }

    show(song){
        this.Song.show(song.id)
            .then(({ song: data }) => {
                this.song = data;
            });
    }

    play(song, event){
        event.preventDefault();
        event.stopPropagation();

        this.Song.show(song.id)
            .then(({ song: data }) => {
                this.song = data;
                this.$rootScope.$emit('play-song', data, this.favorites);
            });
    }

    like(song){
        this.Song.like(song)
            .then(()=>{
                this.Playlist.clearCache();
                this.song.isFavorite = !this.song.isFavorite;
                this.favorites = this.favorites.filter(sng => sng.id !== song.id);
            });
    }

    nextPage(page){
        this.loadFavorites();
    }
}

FavoritesController.$inject = ['$rootScope','$auth','Song','User','Playlist','Paginator'];

export default FavoritesController;