<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User list</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>

    <div class="panel panel-default">
        <div class="panel-heading">Users list
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add user
            </button>
        </div>
        <div class="panel-body">
            <div id="users">
            @foreach($users as $user)
                <div class="user">
                    <p>{{ $user->id }}: {{ $user->name }} - {{ $user->position->name }}</p>
                    <img src="/uploads/short/{{ $user->photo }}">
                </div>
            @endforeach
            </div>

            <button class="btn btn-primary show-more" data-page="2" data-link="/users?page=" data-div="#users">Show more</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $('.show-more').on('click', function() {
            $div = $($(this).data('div')); //div to append
            $link = $(this).data('link'); //current URL

            $page = $(this).data('page'); //get the next page #
            $href = $link + $page; //complete URL
            $.get($href, function (response) { //append data
                $html = $(response).find("#users").html();
                $div.append($html);
            });

            $(this).data('page', (parseInt($page) + 1)); //update page #
        });

        $(function () {
            $("form").on('submit', function (event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.get('/api/token', {}, function (response) {
                    let token = response.token;

                    $.ajax({
                        url: '/api/user',
                        type: 'POST',
                        data: formData,
                        headers: {
                            "Token": token
                        },
                        success: function (data) {
                            console.log(data);
                            alert(data)
                        },
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false
                    }).done(function (data) {
                        console.log(data);
                    });
                });
            });
        });

    </script>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="add-user-form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="token">Token</label>
                            <input type="text" class="form-control" id="token" name="token" placeholder="Token">
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Name</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                        </div>
                        <div class="form-group">
                            <label for="position_id">Position ID</label>
                            <input type="number" class="form-control" id="position_id" name="position_id" placeholder="Position ID">
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" placeholder="Photo">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
