 const showCreativesBtn = document.getElementById('show-creatives');
    const showGigsBtn = document.getElementById('show-gigs');
    const creativesSection = document.getElementById('creatives-section');
    const gigsSection = document.getElementById('gigs-section');

    showCreativesBtn.addEventListener('click', () => {
      creativesSection.style.display = 'block';
      gigsSection.style.display = 'none';

      showCreativesBtn.classList.add('active-filter');
      showCreativesBtn.classList.remove('text-muted');

      showGigsBtn.classList.remove('active-filter');
      showGigsBtn.classList.add('text-muted');
    });

    showGigsBtn.addEventListener('click', () => {
      creativesSection.style.display = 'none';
      gigsSection.style.display = 'block';

      showGigsBtn.classList.add('active-filter');
      showGigsBtn.classList.remove('text-muted');

      showCreativesBtn.classList.remove('active-filter');
      showCreativesBtn.classList.add('text-muted');
    });