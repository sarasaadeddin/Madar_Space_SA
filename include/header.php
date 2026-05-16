<header class="header">
  <div class="container">

    <a href="/Madar_Space_SA/home.php" class="logo">
      Madar<span>.</span>
    </a>

    <nav class="nav">
      <ul class="nav-links">
        <li><a href="/Madar_Space_SA/home.php">Home</a></li>
        <li><a href="/Madar_Space_SA/tasks.php">To-Do</a></li>
        <li><a href="/Madar_Space_SA/journal.php">Journal</a></li>
        <li><a href="/Madar_Space_SA/library.php">Library</a></li>
        <li><a href="/Madar_Space_SA/prayer.php">Tracker</a></li>
      </ul>
    </nav>

    <div class="nav-icons">

      <span class="welcome-user">
        Welcome <?php echo $_SESSION['user_name']; ?> !
      </span>

      <a href="/Madar_Space_SA/php/profile.php">
        <i class="fas fa-user"></i>
      </a>

      <a href="/Madar_Space_SA/php/logout.php">
        <i class="fas fa-right-from-bracket"></i>
      </a>

      <div class="menu-toggle" id="menu-toggle">
        <i class="fa-solid fa-bars"></i>
      </div>

    </div>
  </div>
</header>