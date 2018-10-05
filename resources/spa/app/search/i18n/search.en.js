
let EN = ($translateProvider) => {
  $translateProvider.translations('en', {
    SEARCH_RESULTS_FOR: 'Search results for',
    SEARCH_RESULTS_EMPTY: 'We\'re sorry, but we couldn\'t  find any results for those keywords. Please try something different.',
  });
}

EN.$inject = ['$translateProvider'];

export default EN;