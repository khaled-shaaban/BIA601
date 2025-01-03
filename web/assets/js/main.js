(function() {
  "use strict";

 
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Init isotope layout and filters
   */
  document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
    let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

  });
  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);  

})();

let i = 0;
 // التعامل مع زر "Add Another Item"
document.getElementById('addItemBtn').addEventListener('click', () => {
  // إنشاء عنصر جديد باستخدام نفس القالب
  const newItemDiv = document.createElement('div');
  newItemDiv.classList.add('item', 'mb-3');

  newItemDiv.innerHTML = `
    <div class="my-4 border-1 radius-5 p-3">
      <div class="my-3">
        <label>Item Name</label>
        <input type="text" class="form-control New_Item" placeholder="Item Name" name="items[${i}][name]">
      </div>
      <div class="my-3">
        <label>Item Weight</label>
        <input type="number" class="form-control item-weight" placeholder="Weight (kg)" name="items[${i}][weight]">
      </div>
      <div class="my-3">
        <label>Profit from item</label>
        <input type="number" class="form-control item-value" placeholder="Value" name="items[${i}][profit]">
      </div>
    </div>
  `;

  i++;

  // إدراج العنصر الجديد قبل الزر مباشرة
  const form = document.getElementById('luggageForm');
  form.insertBefore(newItemDiv, document.getElementById('addItemBtn'));
});

document.getElementById('luggageForm').addEventListener('submit', (e) => {
  e.preventDefault();

  var formData = new FormData(e.target);

  const items = [];
  formData.forEach((value, key) => {
    const match = key.match(/^items\[(\d+)\]\[(\w+)\]$/);
    if (match) {
      const index = parseInt(match[1], 10); // Convert string to number
      const property = match[2];
      
      if (!items[index]) {
        items[index] = {}; // Ensure an object exists at the index
      }
      items[index][property] = value;
    }
  });

  var xhttp = new XMLHttpRequest();
  const selectedItems = [];
  var weight = null;

  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      const response = JSON.parse(this.response);

      const binaryItems = response.solution.split('');
      weight = response.weight;
      
      binaryItems.forEach((v, i) => {
        if (v === '1') {
          selectedItems.push(items[i]);
        }
      })

      const weightElement = document.getElementById('result-solution-weight');
      weightElement.innerText = weight

      const selectedItemsUL = document.getElementById('result-solution-items');
      
      selectedItems.forEach((v, i) => {
        const listItem = document.createElement('li');
        selectedItemsUL.appendChild(listItem)
        listItem.innerText = v.name
      })
    }
  };

  xhttp.open("POST", "http://localhost:8000/api/knapsack", true);
  xhttp.send(formData);  
})

 