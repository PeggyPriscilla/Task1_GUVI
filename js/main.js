function loadPage(page) {
  const app = document.getElementById('app');

  // Slide out animation
  app.classList.remove('slide-in');
  app.classList.add('slide-out');

  setTimeout(() => {
    fetch(`pages/${page}.html`)
      .then(res => res.text())
      .then(data => {
        app.innerHTML = data;

        // Slide in after load
        app.classList.remove('slide-out');
        app.classList.add('slide-in');

        // Load specific JS for that page
        const existingScript = document.querySelector(`script[src="js/${page}.js"]`);
        if (existingScript) {
          existingScript.remove(); // Remove old script to force reload
        }
        
        const script = document.createElement('script');
        script.src = `js/${page}.js`;
        script.onload = function() {
          console.log(`Loaded ${page}.js successfully`);
        };
        script.onerror = function() {
          console.error(`Failed to load ${page}.js`);
        };
        document.body.appendChild(script);
      });
  }, 300); // time for slide-out animation
}

// Load default page
window.onload = () => loadPage('register');
