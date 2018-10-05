
const Playlist = (Connection) => {
  const service = Connection.resource('/playlists');

  service.addSong = (params) => {
    return Connection.post({
        url: `/playlists/${params.playlistId}`,
        data: { song: params.songId },
      })
      .then((response) => {
        service.clearCache();
        return Promise.resolve(response);
      });
  };

  service.getFavorites = (params) => {
    return Connection.get({
      url: '/playlists/favorites',
    });
  };
    
  service.getHistory = function(data){
    return Connection.get({
      url: '/playlists/history',
      data,
    })
  };
    
  service.removeSong = function(playlistId, songId){
    return Connection.delete({
        url: `/playlists/${playlistId}/song/${songId}`,
      })
      .then((response) => {
        service.clearCache();

        return Promise.resolve(response);
      });
  };

    return service;
}

Playlist.$inject = ['Connection'];

export default Playlist;