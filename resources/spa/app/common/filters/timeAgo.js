const intervals = [
  { label: 'Year', seconds: 31536000 },
  { label: 'Month', seconds: 2592000 },
  { label: 'Day', seconds: 86400 },
  { label: 'Hour', seconds: 3600 },
  { label: 'Minute', seconds: 60 },
  { label: 'Second', seconds: 0 }
];

let TimeAgo = () => {

  return function (value) {
    if (!value) return '';

    let date = new Date(`${value}Z`);
    
    // For unix time dates
    if (typeof value === 'number') {
      let date = new Date(value);
    }
    
    const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
    const interval = intervals.find(i => i.seconds <= seconds);
    
    if (interval === undefined) {
      return '';
    }

    if (interval.seconds === 0) {
      return 'just now';
    }

    const count = Math.floor(seconds / interval.seconds);

    return `${count} ${interval.label}${count !== 1 ? 's' : ''} ago`;
  };
};

export default TimeAgo;