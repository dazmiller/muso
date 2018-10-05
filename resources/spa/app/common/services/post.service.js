

let Post = ($auth, Connection)=>{
  const service = Connection.resource('/posts');
  const blogService = Connection.resource('/blog');

  service.save = (post) => {
    const url = post.id ? `/posts/${post.id}` : '/posts';
    const form = {
      id              : post.id,
      title           : post.title,
      content         : post.content,
      allow_comments  : post.allow_comments,
      published       : post.published
    };

    if(post.asset instanceof File){
      form.asset = post.asset;
    }

    return Connection.post({ url, form })
      .then((response) => {
        service.clearCache();
        blogService.clearCache();

        return Promise.resolve(response);
      });
  };

    service.latestPublished = (params) =>{
      return blogService.all(params);
    };

    service.published = (id) => {
      return blogService.show(id);
    };

    return service;
}

Post.$inject = ['$auth', 'Connection'];

export default Post;
