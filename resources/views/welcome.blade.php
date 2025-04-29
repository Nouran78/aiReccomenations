
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Chat GPT Laravel </title>
  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>



  <link rel="stylesheet" href="/style.css">


</head>

<body>
<div class="chat">


  <div class="top">
    <img src="https://thumbs.dreamstime.com/b/ai-artificial-intelligence-logo-hands-machine-learning-concept-sphere-grid-wave-binary-code-big-data-innovation-155461047.jpg"style="width:80px;height:60px;margin-top:-0px" alt="Avatar">
    <div>
      <p>Nouran</p>
      <small>Online</small>
    </div>
  </div>


  <!-- Chat -->
  <div class="messages">
    <div class="left message">
      <img src="https://thumbs.dreamstime.com/b/ai-artificial-intelligence-logo-hands-machine-learning-concept-sphere-grid-wave-binary-code-big-data-innovation-155461047.jpg" alt="Avatar">
      <p>Start chatting with Chat GPT AI below!!</p>
    </div>
  </div>
  <!-- End Chat -->

  <!-- Footer -->
  <div class="bottom">
    <form>
      <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
      <button type="submit"></button>
    </form>
  </div>
  <!-- End Footer -->

</div>
</body>

<script>
  //Broadcast messages
  $("form").submit(function (event) {
    event.preventDefault();

    //Stop empty messages
    if ($("form #message").val().trim() === '') {
      return;
    }

    //Disable form
    $("form #message").prop('disabled', true);
    $("form button").prop('disabled', true);

    $.ajax({
      url: "/chat",
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': "{{csrf_token()}}"
      },
      data: {
        "model": "gpt-3.5-turbo",
        "content": $("form #message").val()
      }
    }).done(function (res) {

      //Populate sending message
      $(".messages > .message").last().after('<div class="right message">' +
        '<p>' + $("form #message").val() + '</p>' +
        '<img src="https://thumbs.dreamstime.com/b/ai-artificial-intelligence-logo-hands-machine-learning-concept-sphere-grid-wave-binary-code-big-data-innovation-155461047.jpg"" alt="Avatar">' +
        '</div>');

      //Populate receiving message
      $(".messages > .message").last().after('<div class="left message">' +
        '<img src="https://thumbs.dreamstime.com/b/ai-artificial-intelligence-logo-hands-machine-learning-concept-sphere-grid-wave-binary-code-big-data-innovation-155461047.jpg"" alt="Avatar">' +
        '<p>' + res + '</p>' +
        '</div>');

      //Cleanup
      $("form #message").val('');
      $(document).scrollTop($(document).height());

      //Enable form
      $("form #message").prop('disabled', false);
      $("form button").prop('disabled', false);
    });
  });

</script>
</html>
