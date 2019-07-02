const skillInput = document.getElementById('skill-input');
const skillAddBtn = document.getElementById('skill-add');
const addJobBtn = document.getElementById('addJobBtn');
const skillContainer = document.getElementById('skill-container');
const form = document.getElementById('jobAddForm');


const addSkill = function(e) {
  if (skillInput.value !== "") {
    const div = document.createElement("div");
    div.classList.add("p-1");
    div.innerHTML = `<a class="btn btn-sm btn-dark" href="#" role="button">${
      skillInput.value
    }</a>`;
    skillContainer.appendChild(div);
    skillInput.value = "";
  }
};

skillAddBtn.addEventListener("click", addSkill);

document.addEventListener("keydown", function(e) {
  if (e.keyCode === 13) {
    e.preventDefault();
    addSkill();
  }
});

addJobBtn.addEventListener("click", function(e) {
  e.preventDefault();
  const skillContainerNodes = skillContainer.children;
  const skills = [];
  for (let element of skillContainerNodes) {
    skills.push(element.childNodes[0].innerHTML);
  }
  const hInput = document.createElement("input");
  hInput.type = "hidden";
  hInput.name = "skills";
  hInput.value = JSON.stringify(skills);
  form.appendChild(hInput);
  form.submit();
});

skillContainer.addEventListener("click", function(e) {
  e.preventDefault();
  if (e.target.classList.contains("btn")) {
    e.target.parentNode.remove();
  }
});


document.addEventListener("click", function(e) {
  if(e.target.id === "more") {
    if(e.target.innerHTML === "More Details") {
      e.target.innerHTML = "Hide Details";
    }
    else {
      e.target.innerHTML = "More Details";
    }
  }
});