
class HistoryController {
    constructor($rootScope, $auth, Song, User, Playlist, Paginator){
        this.$rootScope = $rootScope;
        this.Song = Song;
        this.Paginator = Paginator;
        this.Playlist = Playlist;

        Paginator.start();
        this.loadHistory();
    }

    loadHistory(){

        this.Playlist.getHistory({ page: this.Paginator.getPage() })
            .then(({ history, meta }) => {
                if(this.Paginator.getPage() > 0 && this.history){
                    this.history.push(...history);
                }else{
                    this.history = history;
                }

                this.total  = meta.total;
                this.current= this.history.length;
            });
    }

    show(song){
        this.Song.show(song.id)
            .then(({ song: data })=>{
                this.song = data;
            });
    }

    play(song, event){
        event.preventDefault();
        event.stopPropagation();

        this.Song.show(song.id)
            .then(({ song: data }) => {
                this.song = data;
                this.$rootScope.$emit('play-song', data, [song]);
            });
    }

    like(song){
        this.Song.like(song)
            .then(()=>{
                this.song.isFavorite = !this.song.isFavorite;
            });
    }

    nextPage(page){
        this.loadHistory();
    }
}

HistoryController.$inject = ['$rootScope','$auth','Song','User','Playlist','Paginator'];

export default HistoryController;