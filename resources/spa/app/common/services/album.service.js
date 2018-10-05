
const Album = (Connection) => {
  const service = Connection.resource('/albums');

  service.save = (album)=>{
    const url = album.id ? `/albums/${album.id}` : '/albums';
    const form = {
      id          : album.id,
      genre_id    : album.genre_id,
      title       : album.title,
      description : album.description,
      release_date: `${album.release_date.getUTCFullYear()}-${album.release_date.getUTCMonth() + 1}-${album.release_date.getUTCDate()}`,
      published   : album.published
    };

    if(album.image instanceof File){
      form.image = album.image;
    }

    return Connection.post({ url, form })
      .then((response) => {
        service.clearCache();

        return Promise.resolve(response);
      });    
  }

  service.saveTrack = (track) => {
    const url = track.id
      ? `/albums/${track.album_id}/songs/${track.id}`
      : `/albums/${track.album_id}/songs`;
    const form = {
      id          : track.id,
      album_id    : track.album_id,
      title       : track.title,
      description : track.description,
      lyric       : track.lyric,
      tags        : track.tags.join(','),
    };

    if(track.audio instanceof File){
      form.audio = track.audio;
    }

    return Connection.post({ url, form })
      .then((response) => {
        service.clearCache();

        return Promise.resolve(response);
      });
  }

  service.removeTrack = (track)=>{
    return Connection.delete({
        url: `/albums/${track.album_id}/songs/${track.id}`
      })
      .then((response) => {
        service.clearCache();

        return Promise.resolve(response);
      });
  }

  service.discover = ()=>{
    return Connection.get({
      url: '/discovers',
    });
  }
  
  // Returns a published album
  service.published = (id) => {
    return service.show(`${id}/published`);
  }

  service.search = (query) => {
    return Connection.get({
      url: '/search/albums',
      data: { query }
    });
  };

  return service;
}

Album.$inject = ['Connection'];

export default Album;