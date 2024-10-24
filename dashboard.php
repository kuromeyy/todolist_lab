<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A simple and efficient to-do list system for managing your events and tasks.">
    <meta name="keywords" content="event management, to-do list, task management, login, register">
    <title>Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gradient Background: Light Grey to White */
        body {
            background: linear-gradient(to bottom, #e5e7eb, #ffffff); /* Grey to white */
        }

        .btn {
            transition: transform 0.2s ease-in-out, background-color 0.2s ease;
        }

        .btn:hover {
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<!-- Content Section -->
<div class="flex flex-col justify-center items-center flex-grow py-10">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-800 mb-6">Welcome to the To-Do List System</h1>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <a href="login.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg btn hover:bg-blue-500">Login</a>
            <a href="register.php" class="bg-green-600 text-white px-6 py-2 rounded-lg btn hover:bg-green-500">Register</a>
        </div>
    </div>
</div>

<!-- Footer Section -->
<footer class="bg-gray-800 text-white py-4 mt-auto">
    <div class="container mx-auto text-center">
        <p class="text-sm">© 2024 Kellen Valerie. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
