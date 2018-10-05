
class SongsController {

  constructor($state, $rootScope, $auth, Song, Album, User) {
    this.songId = $state.params.id;
    this.downloable = Configurations.APP_DOWNLOAD_SONG_FILE;

    if($auth.isAuthenticated()){
      User.requestCurrentUser()
        .then((data)=>{
            this.currentUser = data;
        });
    }

    Song.show(this.songId)
      .then(({ song: data }) => {
        this.song = data;
        this.album = data.album;
        this.song.tagsText = data.tags.map(tag => tag.name).join(',');

        return Promise.resolve(data);
      })
      .then((data) => Album.published(data.album.id))
      .then(({ published: data }) => {
        this.album = data;
      })
      .then(() => {
        if (this.song.tags.length > 0) {
          Song.getByTag(this.song.tags[0].name)
            .then(({ songs: similar }) => {
              if (similar.length > 5) {
                similar = similar.slice(0, 5);
              }
              this.similar = similar;
            });
        }

        Song.getUsersWhoLikedSong(this.song)
          .then(({ users }) => {
            this.users = users;
          });
      });

    this.comment = {};
    this.Song = Song;
    this.$rootScope = $rootScope;
  }

  play(song){
    const songs = this.album.songs.map(sng => ({
      id: sng.id,
      title: sng.title,
      author: this.album.author,
      album: {
        image: this.album.image,
      },
      sound: {
        ...sng.file,
      },
     }));
    this.$rootScope.$emit('play-song', song, songs);
  }

  playSongFromAlbum(song, event) {
    event.preventDefault();
    event.stopPropagation();
    
    this.play({
      ...song,
      author: this.album.author,
      album: {
        image: this.album.image,
      },
      sound: {
        ...song.file,
      },
    });
  }

  like(song) {
    this.Song.like(song)
      .then(()=>{
        this.song.isFavorite = !this.song.isFavorite;
      });
  }

  download(song) {
    this.Song.download(song);
  }
}

SongsController.$inject = ['$state', '$rootScope', '$auth', 'Song', 'Album', 'User'];

export default SongsController;