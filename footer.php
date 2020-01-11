<script>
  $(document).ready(function() {
    $('#showLogIn').click(function() {
      $('#loginFieldset').show();
      $('#signupFieldset').hide();
      $('#showSignUp').toggleClass('btn btn-primary');
      $(this).toggleClass('btn btn-primary');
    });
    $('#showSignUp').click(function() {
      $('#loginFieldset').hide();
      $('#signupFieldset').show();
      $('#showLogIn').toggleClass('btn btn-primary');
      $(this).toggleClass('btn btn-primary');
    });
    $('#diary').change(function() {
      $.ajax({
        method : "POST",
        url : "update_database.php",
        data : { content : $('#diary').val() },
        success : function ( data ) {
          alert(data);
        },
        error: function() {
          alert("REQUEST_ERROR");
        }
      });
    });
  });
</script>
