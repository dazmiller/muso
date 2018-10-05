
const Video = ($state, Connection) => {
    const service = Connection.resource('/videos');
    const discoverGenreService = Connection.resource('/discovers/genre');
    const discoverTagService = Connection.resource('/discovers/tag');
  
    service.like = (video) => {
      return service.show(`${video.id}/likeable`)
        .then((response) => {
          service.clearCache();
          return Promise.resolve(response);
        })
        .catch((response) => {
          if (response.error === 'token_not_provided') {
            $state.go('public.auth');
          }
        });
    };
  
    service.getByGenre = (genre, data) => {
      return discoverGenreService.show(genre, data);
    };
  
    service.getByTag = (tag, params) => {
      return discoverTagService.show(tag, params);
    };
  
    service.getUsersWhoLikedSong = (video) => {
      return Connection.get({
        url: `/discovers/users/${video.id}/liked`,
      });
    };
  
    service.search = (query) => {
      return Connection.get({
        url: '/search/videos',
        data: { query }
      });
    };
  
    service.download = (video) => {
      return Connection.get({
        url: `/video/${video.id}/download`,
      });
    };
  
    return service;
  }
  
  Video.$inject = ['$state', 'Connection'];
  
  export default Video;