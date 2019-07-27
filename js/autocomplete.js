function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener('input', function(e) {
    var a,
      b,
      i,
      val = this.value;
    /*close any already open lists of autocompleted values*/
    closeAllLists();
    if (!val) {
      return false;
    }
    currentFocus = -1;
    /*create a DIV element that will contain the items (values):*/
    a = document.createElement('DIV');
    a.setAttribute('id', this.id + 'autocomplete-list');
    a.setAttribute('class', 'autocomplete-items');
    /*append the DIV element as a child of the autocomplete container:*/
    this.parentNode.appendChild(a);
    /*for each item in the array...*/
    for (i = 0; i < arr.length; i++) {
      /*check if the item starts with the same letters as the text field value:*/
      if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
        /*create a DIV element for each matching element:*/
        b = document.createElement('DIV');
        /*make the matching letters bold:*/
        b.innerHTML = '<strong>' + arr[i].substr(0, val.length) + '</strong>';
        b.innerHTML += arr[i].substr(val.length);
        /*insert a input field that will hold the current array item's value:*/
        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
        /*execute a function when someone clicks on the item value (DIV element):*/
        b.addEventListener('click', function(e) {
          /*insert the value for the autocomplete text field:*/
          inp.value = this.getElementsByTagName('input')[0].value;
          /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
          closeAllLists();
        });
        a.appendChild(b);
      }
    }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener('keydown', function(e) {
    var x = document.getElementById(this.id + 'autocomplete-list');
    if (x) x = x.getElementsByTagName('div');
    if (e.keyCode == 40) {
      /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
      currentFocus++;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 38) {
      //up
      /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
      currentFocus--;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 13) {
      /*If the ENTER key is pressed, prevent the form from being submitted,*/
      e.preventDefault();
      if (currentFocus > -1) {
        /*and simulate a click on the "active" item:*/
        if (x) x[currentFocus].click();
      }
    }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = x.length - 1;
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add('autocomplete-active');
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove('autocomplete-active');
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
    var x = document.getElementsByClassName('autocomplete-items');
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener('click', function(e) {
    closeAllLists(e.target);
  });
}

const cities = [
  'Agartala',
  'Aizawl',
  'Bengaluru',
  'Bhopal',
  'Bhubaneswar',
  'Chandigarh',
  'Chennai',
  'Dehradun',
  'Dispur',
  'Gandhinagar',
  'Gangtok',
  'Hyderabad',
  'Imphal',
  'Itanagar',
  'Jaipur',
  'Kohima',
  'Kolkata',
  'Lucknow',
  'Mumbai',
  'National Capital Region (NCR)',
  'New Delhi',
  'Panaji',
  'Patna',
  'Raipur',
  'Ranchi',
  'Shillong',
  'Shimla',
  'Thiruvananthapuram'
];

const designation = [
  'Assessor',
  'Auditor',
  'Budget analyst',
  'Cash manager',
  'Chief financial officer',
  'Controller',
  'Credit manager',
  'Tax specialist',
  'Treasurer',
  'Benefits officer',
  'Compensation analyst',
  'Employee relations specialist',
  'HR coordinator',
  'HR specialist',
  'Retirement plan counselor',
  'Staffing consultant',
  'Union organizer',
  'Certified financial planner',
  'Chartered wealth manager',
  'Credit analyst',
  'Credit manager',
  'Financial analyst',
  'Hedge fund manager',
  'Hedge fund principal',
  'Hedge fund trader',
  'Investment advisor',
  'Investment banker',
  'Investor relations officer',
  'Investor relations officer',
  'Loan officer',
  'Mortgage banker',
  'Mutual fund analyst',
  'Portfolio management marketing',
  'Portfolio manager',
  'Ratings analyst',
  'Stockbroker',
  'Trust officer',
  'Business systems analyst',
  'Content manager',
  'Content strategist',
  'Database administrator',
  'Digital marketing manager',
  'Full stack developer',
  'Information architect',
  'Marketing technologist',
  'Mobile developer',
  'Project manager',
  'Social media manager',
  'Software engineer',
  'Systems engineer',
  'Software developer',
  'Systems administrator',
  'User interface specialist',
  'Web analytics developer',
  'Web developer',
  'Webmaster',
  'Actuary',
  'Claims adjuster',
  'Damage appraiser',
  'Insurance adjuster',
  'Insurance agent',
  'Insurance appraiser',
  'Insurance broker',
  'Insurance claims examiner',
  'Insurance investigator',
  'Loss control specialist',
  'Underwriter',
  'Business broker',
  'Business transfer agent',
  'Commercial appraiser',
  'Commercial real estate agent',
  'Commercial real estate broker',
  'Real estate appraiser',
  'Real estate officer',
  'Residential appraiser',
  'Residential real estate agent',
  'Residential real estate broker',
  'Marketing Consultant',
  'Social Media Analyst',
  'Social Media Designer',
  'Graphics Designer',
  'Advertising Manager',
  'Associate Manager',
  'Sales and Catering Manager',
  'Branch Manager',
  'Business Banking Loan Administration Manager',
  'Business Banking Officer',
  'Personal Banker',
  'Art Director',
  'Copywriter',
  'Developer',
  'Promotions Manager',
  'Promotions Coordinator',
  'Media Assistant',
  'Traffic Manager',
  'Content Writer',
  'SEO Manager',
  'Web Content Marketing Specialist',
  'Digital Content Specialist',
  'Digital Marketing Manager',
  'Email Marketer',
  'Public Relations Manager',
  'Sales Assistant',
  'Sales Associate',
  'Relationship Manager',
  'Account Executive'
];

/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById('location'), cities);
autocomplete(document.getElementById('designation'), designation);
