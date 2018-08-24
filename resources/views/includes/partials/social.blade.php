<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
  FB.init({
  appId : '{!! config('seotools.socialize.facebook.app_id') !!}',
  status : true, // check login status
  cookie : true, // enable cookies to allow the server to access the session
  xfbml : true // parse XFBML
  });
  };
  (function() {
  var e = document.createElement('script');
  e.src = document.location.protocol + '//connect.facebook.net/vi_VN/all.js';
  e.async = true;
  document.getElementById('fb-root').appendChild(e);
  }());
</script>

<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: 'vi'}
</script>