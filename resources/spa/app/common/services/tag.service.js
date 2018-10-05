
let Tag = (Connection) => {
  let service = Connection.resource('/tags');

  // Returns the tags used by the songs
  service.song = (song) => {
    return service.show('song');
  };

  return service;
}

Tag.$inject = ['Connection'];

export default Tag;