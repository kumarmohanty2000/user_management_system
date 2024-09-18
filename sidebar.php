<aside class="sidebar">
    <div  class="home">
        <a href="home.php">My Dashboard</a>
    </div>
    <ul>
        <li class="<?php echo ($currentPage == 'profile') ? 'active' : ''; ?>">
            <a href="profile.php">Profile</a>
        </li>
        <li class="<?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
            <a href="my_task.php">My Tasks</a>
        </li>
        <li class="<?php echo ($currentPage == 'add_task') ? 'active' : ''; ?>">
            <a href="add_task.php">Add Task</a>
        </li>
        <li>
            <a href="logout.php">Logout</a>
        </li>
    </ul>
</aside>
