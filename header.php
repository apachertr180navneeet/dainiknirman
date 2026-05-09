<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'NIRMA PRAKASHAN') ?></title>
  <link rel="icon" type="image/png" href="images/logo.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@0.469.0/font/lucide.min.css">
 <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="min-h-screen bg-background flex flex-col">
    
    <!-- Header -->
    <header class="header">
      <div class="header-container">
        <!-- Logo -->
        <a href="index.html" class="header-logo">
          <img src="images/logo.png" alt="NIRMA PRAKASHAN">
          <span>Dainik Nirman</span>
        </a>

        <!-- Navigation -->
        <nav class="header-nav">
          <a href="index.html" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Home</a>
          <a href="#contest" class="nav-link <?= $activePage === 'contest' ? 'active' : '' ?>">Contests</a>
           <a href="#books" class="nav-link <?= $activePage === 'books' ? 'active' : '' ?>">Books</a>
          <a href="#contact" class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>">Contact</a>
        </nav>

        <!-- Actions -->
        <div class="header-actions">
          <!-- Search -->
          <div class="header-search">
            <input type="text" placeholder="Search books...">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.3-4.3"></path>
            </svg>
          </div>

          <!-- Cart -->
          <a href="#" class="header-icon" title="Cart">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="8" cy="21" r="1"></circle>
              <circle cx="19" cy="21" r="1"></circle>
              <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
          </a>

          <!-- Mobile Menu Toggle -->
          <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="4" x2="20" y1="12" y2="12"></line>
              <line x1="4" x2="20" y1="6" y2="6"></line>
              <line x1="4" x2="20" y1="18" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div class="mobile-menu" id="mobileMenu">
        <a href="index.html" class="mobile-nav-link">Home</a>
        <a href="#books" class="mobile-nav-link">Books</a>
        <a href="#contest" class="mobile-nav-link">Contests</a>
        <a href="#" class="mobile-nav-link">About</a>
        <a href="#" class="mobile-nav-link">Contact</a>
      </div>
    </header>