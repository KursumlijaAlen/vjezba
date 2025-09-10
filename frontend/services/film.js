var FilmService = {
    delete_film: function(film_id) {
        if (
          confirm(
            "Do you want to delete film with the id " + film_id + "?"
          ) == true
        ) {
          console.log("TODO Perform deletion logic");
        }
    },
    edit_film: function(film_id){
        console.log("Get film with provided id, open modal and populate modal fields with data returned from the database");
        alert("Opened");
    },

getMovies: function(){
  RestClient.get('film/performance', function(response) {
    const filmperformancebody = document.getElementById("film-performance-body");

    filmperformancebody.innerHTML = "";

    response.forEach(function(film) {
      const row = document.createElement("tr");
      row.innerHTML = `
      <td class="text-center">
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-warning" onclick="FilmService.edit_film(${film.category_id})">
            Edit
          </button>
          <button type="button" class="btn btn-danger" onclick="FilmService.delete_film(${film.category_id})">
            Delete
          </button>
        </div>
      </td>
      <td>${film.category_id}</td>
      <td>${film.name}</td>
      <td>${film.total_number_of_movies}</td>
      `;
      filmperformancebody.appendChild(row);
    });
  }, function(error) {
    console.error("Error occurred while fetching movies: ", error);
  });
}
}