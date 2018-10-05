
class DiscoverController {

  constructor($rootScope, Album, Song) {
    Album.discover()
      .then(({ discover: data }) => {
        this.latests = data.latests;
        this.recommendations = data.recommendations;
        this.topTen = data.topten;
        this.artists = data.artists;
      });

    this.$rootScope = $rootScope;
    this.Song = Song;
  }

  play(song, playlist) {
    this.$rootScope.$emit('play-song', song, playlist);
  }

  like(song) {
    this.Song.like(song)
      .then(() => {
        song.isFavorite = !song.isFavorite;
      });
  }
    
}

DiscoverController.$inject = ['$rootScope', 'Album', 'Song'];

export default DiscoverController;