/**
 * Returns the url for a song to share on facebook, the URL
 * is a laravel url for SSR.
 */
let SetFullSongUrl = () => {
  return function (songId, wordwise, max, tail) {
    return `${process.env.APP_URL}/web/songs/${songId}`;
  };
};

export default SetFullSongUrl;
