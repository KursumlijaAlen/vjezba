var Constants = {
  get_api_base_url: function () {
    if(location.hostname == 'localhost'){
      return "http://localhost/web-programming-makeup/backend/rest/";
    } else {
      return "";
    }
  }
};