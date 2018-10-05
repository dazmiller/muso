
export default function fetchData(url, options) {
  return fetch(url, options)
    .then(response => response.json());
}
