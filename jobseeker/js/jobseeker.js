const skillAddBtn = document.getElementById("skill-add");
const skillInput = document.getElementById("skill-input");
const skillContainer = document.getElementById("skill-container");
const form = document.getElementById("profileEditForm");
const updateBtn = document.getElementById("updatebtn");
const checkbox = document.getElementById('fresher');

// Initialize BSCustomFileInput plugin for showing File Input name
window.addEventListener("DOMContentLoaded", event => {
  bsCustomFileInput.init();
});

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

updateBtn.addEventListener("click", function(e) {
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

checkbox.addEventListener('click', function(e) {
    if(checkbox.checked === true) {
        document.getElementById('present_company').disabled = true;
        document.getElementById('designation').disabled = true;
        document.getElementById('salary').disabled = true;
        document.getElementById('experience').disabled = true;
        
    } else {
        document.getElementById('present_company').disabled = false;
        document.getElementById('designation').disabled = false;
        document.getElementById('salary').disabled = false;
        document.getElementById('experience').disabled = false;
    }
});

