
let Comment = (Connection) => {
    return Connection.resource('/comments');
}

Comment.$inject = ['Connection'];

export default Comment;