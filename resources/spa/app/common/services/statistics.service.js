
const Statistics = (Connection) => {
  const service = Connection.resource('/statistics');

  return service;
}

Statistics.$inject = ['Connection'];

export default Statistics;