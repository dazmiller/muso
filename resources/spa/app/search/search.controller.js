
class SongsController {

  constructor($state, Song, Album, User) {
    this.query = $state.params.query;
    this.totals = {
      albums: null,
      artists: null,
      songs: null,
    };

    Song.search(this.query)
      .then(({ songs, meta }) => {
        this.songs = songs;
        this.totals = {
          ...this.totals,
          songs: meta.total,
        };
      });

    Album.search(this.query)
      .then(({ albums, meta }) => {
        this.albums = albums;
        this.totals = {
          ...this.totals,
          albums: meta.total,
        };
      });
    
    User.searchAuthors(this.query)
      .then(({ artists, meta }) => {
        this.artists = artists;
        this.totals = {
          ...this.totals,
          artists: meta.total,
        };
      });
  }
}

SongsController.$inject = ['$state', 'Song', 'Album', 'User'];

export default SongsController;