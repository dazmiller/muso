

const Genre = (Connection)=>{
  return Connection.resource('/genres');
}

Genre.$inject = ['Connection'];

export default Genre;
