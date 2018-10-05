
class AlbumsPublicController {

  constructor($state, $rootScope, Album) {
    Album.published($state.params.id)
      .then(({ published: data }) => {
        this.album = data;
      });

    this.comment = {};
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

  playAll() {
    const song = this.album.songs[0];


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
}

AlbumsPublicController.$inject = ['$state', '$rootScope', 'Album'];

export default AlbumsPublicController;