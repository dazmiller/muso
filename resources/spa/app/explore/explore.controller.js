
class ExploreController {
  constructor($state, $rootScope, $translate, Genre, Paginator, Song, Tag) {
    this.$state = $state;
    this.$rootScope = $rootScope;
    this.$translate = $translate;
    this.Paginator = Paginator;
    this.Genre = Genre;
    this.Tag = Tag;
    this.Song = Song;
    this.popular = 'week';

    Paginator.start();

    this.loadSongs();
    this.loadTags();
    this.loadGenres();
  }

  loadSongs() {
    if (this.$state.params.genre) {
        this.loadSongsByGenre(this.$state.params.genre);
    } else {
        this.loadSongByTag(this.$state.params.tag);
    }
  }

  loadSongsByGenre(id){
    let params = {
      page: this.Paginator.getPage(),
      popular: this.popular,
    };

    this.Song.getByGenre(id, params)
      .then((response) => {
        if (this.selectedGenre && this.selectedGenre === id){
            this.songs = [...this.songs, ...response.songs];
        }else{
            this.songs  = response.songs;
            this.Paginator.start();
        }

        this.selectedGenre = id;
        if (this.genres && this.genres.length > 0) {
            this.genre = this.genres.find(genre => `${genre.id}` === this.selectedGenre);
        }
      });
  }

  loadSongByTag(tag) {
    let params = {
      page: this.Paginator.getPage(),
      popular: this.popular,
    };

    this.Song.getByTag(tag, params)
      .then((response) => {
        if (this.selectedTag && this.selectedTag === tag) {
          this.songs = [...this.songs, ...response.songs];
        } else {
          this.songs = response.songs;
          this.Paginator.start();
        }

        this.selectedTag = tag;
      })
  }

  loadGenres() {
    this.Genre.all()
      .then(({ genres }) => {
        this.genres = [
          { id: 'all', name: this.$translate.instant('GENRES_ALL') },
          ...genres
        ];

        if (this.selectedGenre) {
          this.genre = genres.find(genre => `${genre.id}` === this.selectedGenre);
        }
      });
  }
    
  loadTags() {
    this.Tag.song()
      .then((response) => {
          this.tags = response.tags;
      });
  }

  loadGenre(id){
    this.Paginator.start();
    this.loadSongsByGenre(id);
  }

  nextPage(){
    this.loadSongs();
  }

  play(song){
    this.$rootScope.$emit('play-song',song);
  }

  like(song){
    this.Song.like(song);
  }

  setPopularity(popular) {
    this.popular = popular;
    this.songs = [];
    this.Paginator.start();
    this.loadSongs();
  }
}

ExploreController.$inject = ['$state', '$rootScope', '$translate', 'Genre', 'Paginator', 'Song', 'Tag'];

export default ExploreController;