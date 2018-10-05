
class PlaylistsController {
  constructor($rootScope, $auth, $state, Song, User, Playlist, Paginator) {

    this.playlist = {
      id  : $state.params.id,
      songs: [],
    };

    this.$rootScope = $rootScope;
    this.Song = Song;
    this.Playlist = Playlist;

    this.loadPlaylist();
  }

  loadPlaylist() {
    this.Playlist.show(this.playlist.id)
      .then(({ playlist })=>{
        this.playlist = playlist;
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
        this.$rootScope.$emit('play-song', data, this.playlist.songs);
      });
  }

  like(song){

    this.Song.like(song)
      .then(()=>{
        this.song.isFavorite = !this.song.isFavorite;
      });
  }

  removeFromList(playlist, song, event) {
    event.preventDefault();
    event.stopPropagation();

    this.Playlist.removeSong(playlist.id, song.id)
      .then(() => {
        this.playlist.songs = this.playlist.songs.filter(item => song.id !== item.id);
      });
  }
}

PlaylistsController.$inject = ['$rootScope', '$auth', '$state', 'Song', 'User', 'Playlist'];

export default PlaylistsController;