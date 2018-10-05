import AbstractController from '../../common/controllers/abstract.controller';


class MyMusicController extends AbstractController{

  constructor(){
    super(MyMusicController.$inject, arguments);
    
    this.isForm = true;
    if (this.$state.params.toAdmin === 'admin') {
      this.paths = {
        all: 'admin.albums.all',
        published: 'admin.albums.published',
        unpublished: 'admin.albums.unpublished',
      };
    } else {
      this.paths = {
        all: 'authors.mymusic.all',
        published: 'authors.mymusic.published',
        unpublished: 'authors.mymusic.unpublished',
      };
    }

    this.$scope.$watch('mymusic.album.image', (newValue, oldValue) => {
      if(newValue && newValue instanceof File){
        this.showImage(newValue);
      }
    });

    this.$scope.$watch('mymusic.track.audio', (newValue, oldValue) => {
      if(newValue && newValue instanceof File){
        this.track.sound = newValue.name;
      }
    });
  }

  add(){
    this.album = {
      release_date : new Date(),
    };

    this.Genre.all()
      .then(({ genres }) => {
        this.genres = genres;
      });
  }

  save(album){
    //if published check image and songs
    if(album.published){
      if(!album.image && (!album.songs || album.songs.length === 0)){
          return;
      }
    }

    this.Album.save(album)
      .then(({ album: data }) => {
        data.release_date = new Date(data.release_date);

        if(data.image){
          data.art = data.image;
        }

        this.album = data;
    });
  }

  show(params){
    this.track = {
      tags: [],
    };

    this.Genre.all()
      .then(({ genres }) => {
        this.genres = genres;
      });

    this.Album.show(params.id)
      .then(({ album: data }) => {
        if(data.release_date){
          data.release_date = new Date(data.release_date);
        }else{
          data.release_date = new Date();
        }

        if(data.image){
          data.art = data.image;
        }

        this.album = data;
      });
  }


  editTrack(track, event){
    event.preventDefault();
    event.stopPropagation();

    this.toggleTrackForm();
    this.track = { ...track };
    this.track.tags = this.parseTags(track.tags);
    this.track.sound = track.file && track.file.name;
  }

  saveTrack(track) {
    track.album_id = this.album.id;

    this.Album.saveTrack(track)
      .then(({ song: data }) => {
        const index = this.album.songs.findIndex(song => data.id === song.id);

        if (index > -1) {
          this.album.songs[index] = {
            ...data,
            tags: this.track.tags.map(name => ({ name })),
          };
        } else {
          this.album.songs.push(data);
        }

        this.toggleTrackForm('close');
      });
  }

  removeTrack(track, event){
    event.preventDefault();
    event.stopPropagation();
    
    var confirm = this.$mdDialog.confirm()
      .title(this.$translate.instant('ARE_YOU_SURE'))
      .textContent(this.$translate.instant('ALBUM_DELETE_SONG'))
      .ariaLabel('Delete song')
      .targetEvent(event)
      .ok(this.$translate.instant('YES'))
      .cancel(this.$translate.instant('NO'));

    this.$mdDialog.show(confirm)
        .then(() => {
          track.album_id = this.album.id;

          this.Album.removeTrack(track)
            .then(({ data}) => {
              var index = this.album.songs.indexOf(track);
              if (index > -1) {
                this.album.songs.splice(index, 1);
              }
            });
        });
  }

  toggleTrackForm(track = { tags: [] }){

    if(track !== 'close'){
      this.track = track;
    }

    this.$mdSidenav('track-form').toggle();
  }

  showImage(file){
    let reader = new FileReader();
    this.album.art = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    reader.onload = (event) => {
      let img = document.getElementById('album-image');
      img.src = event.target.result;
    }
    reader.readAsDataURL(file);
  }

  play(song, event){
    event.preventDefault();
    event.stopPropagation();

    const data = {
      ...song,
      author: this.album.author,
      album: {
        image: this.album.image,
      },
      sound: song.file.url,
    };
    this.$rootScope.$emit('play-song', data, [data]);
  }

  parseTags(tags) {
    if (!tags) {
      return [];
    }
    
    return tags.map(tag => tag.name);
  }

  isPublishedEnabled() {
    return this.album && this.album.songs && this.album.songs.length > 0 && this.album && this.album.art && this.album && this.album.art;
  }

  redirectTo(path) {
    this.$state.go(this.paths[path]);
  }
}

//IMPORTANT: $state service should always be injected,
//its required to execute the correct method
MyMusicController.$inject = ['$state','$scope','$rootScope','$mdSidenav','$mdDialog','$translate','Album','Genre'];

export default MyMusicController;