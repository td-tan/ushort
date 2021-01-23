@extends('layouts.default')
@section('title', 'Home')

@section('content')
<div class="container">
    <form id="login" class="needs-validation">
        <div class="form-group">
            <label for="uname">Username:</label>
            <input type="text" class="form-control" id="uname" placeholder="Enter username" name="uname" required>
            <div class="valid-feedback"></div>
            <div class="invalid-feedback">Fill in your username</div>
        </div>
        <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pswd" required>
            <div class="valid-feedback"></div>
            <div class="invalid-feedback">Fill in your password</div>
        </div>
        <div class="form-group form-check">
            <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="remember"> Remember me
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Log In 
            <div class="spinner-border" role="status" hidden>
                <span class="sr-only">Loading...</span>
            </div>
        </button>
    </form>
</div>

<div id="dashboard" class="container" hidden>
    <h2>Dashboard, Welcome </h2>
    <p>Filter:</p>  
    <input class="form-control" id="search" type="text" placeholder="Search..">
    <br>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Link</th>
          <th>Short Link</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody id="ltable" class="table table-bordered table-striped">
      </tbody>
    </table>
    
    <div id="data-loading" class="spinner-border" role="status" hidden>
        <span class="sr-only">Loading...</span>
    </div>
</div>
@endsection

@section('script')
<script src="/js/app.js"></script>
@endsection