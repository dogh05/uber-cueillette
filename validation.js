// js/validation.js

// Validation en temps réel du pseudo
function validatePseudo(pseudo) {
    const regex = /^[A-Za-z]+$/;
    return regex.test(pseudo);
}

// Validation du mot de passe
function validatePassword(password) {
    const regex = /^.{8,}[$#]$/;
    return regex.test(password);
}

// Validation du CIN
function validateCIN(cin) {
    const regex = /^\d{8}$/;
    return regex.test(cin);
}

// Inscription agriculteur
document.getElementById('formAgriculteur')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Récupération des valeurs
    const nom = document.getElementById('agriNom').value.trim();
    const prenom = document.getElementById('agriPrenom').value.trim();
    const cin = document.getElementById('agriCin').value.trim();
    const email = document.getElementById('agriEmail').value.trim();
    const adresse = document.getElementById('agriAdresse').value.trim();
    const pseudo = document.getElementById('agriPseudo').value.trim();
    const password = document.getElementById('agriPassword').value;
    
    // Validations
    if (!nom || !prenom) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    if (!validateCIN(cin)) {
        alert('CIN invalide : doit contenir exactement 8 chiffres');
        return;
    }
    
    if (!email.includes('@')) {
        alert('Email invalide');
        return;
    }
    
    if (!adresse) {
        alert('Veuillez saisir votre adresse');
        return;
    }
    
    if (!validatePseudo(pseudo)) {
        alert('Pseudo invalide : lettres uniquement (pas de chiffres ni caractères spéciaux)');
        return;
    }
    
    if (!validatePassword(password)) {
        alert('Mot de passe invalide : 8 caractères minimum, doit se terminer par $ ou #');
        return;
    }
    
    // Envoi des données
    const formData = new FormData();
    formData.append('type', 'agriculteur');
    formData.append('nom', nom);
    formData.append('prenom', prenom);
    formData.append('cin', cin);
    formData.append('email', email);
    formData.append('adresse', adresse);
    formData.append('pseudo', pseudo);
    formData.append('password', password);
    
    try {
        const response = await fetch('php/inscription.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            // Fermer le modal et réinitialiser le formulaire
            bootstrap.Modal.getInstance(document.getElementById('inscriptionModal')).hide();
            document.getElementById('formAgriculteur').reset();
        } else {
            alert('Erreur : ' + data.message);
        }
    } catch (error) {
        alert('Erreur de connexion au serveur');
    }
});

// Inscription ouvrier
document.getElementById('formOuvrier')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nom = document.getElementById('ouvNom').value.trim();
    const prenom = document.getElementById('ouvPrenom').value.trim();
    const cin = document.getElementById('ouvCin').value.trim();
    const email = document.getElementById('ouvEmail').value.trim();
    const pseudo = document.getElementById('ouvPseudo').value.trim();
    const password = document.getElementById('ouvPassword').value;
    const description = document.getElementById('ouvDescription').value.trim();
    const photo = document.getElementById('ouvPhoto').files[0];
    
    // Validations
    if (!nom || !prenom) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    if (!validateCIN(cin)) {
        alert('CIN invalide : doit contenir exactement 8 chiffres');
        return;
    }
    
    if (!email.includes('@')) {
        alert('Email invalide');
        return;
    }
    
    if (!validatePseudo(pseudo)) {
        alert('Pseudo invalide : lettres uniquement (pas de chiffres ni caractères spéciaux)');
        return;
    }
    
    if (!validatePassword(password)) {
        alert('Mot de passe invalide : 8 caractères minimum, doit se terminer par $ ou #');
        return;
    }
    
    if (!description) {
        alert('Veuillez saisir une description');
        return;
    }
    
    if (!photo) {
        alert('Veuillez sélectionner une photo');
        return;
    }
    
    // Vérifier le type de fichier
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!allowedTypes.includes(photo.type)) {
        alert('Format de photo non supporté. Utilisez JPEG, PNG ou GIF.');
        return;
    }
    
    // Vérifier la taille (max 2MB)
    if (photo.size > 2 * 1024 * 1024) {
        alert('La photo ne doit pas dépasser 2MB');
        return;
    }
    
    const formData = new FormData();
    formData.append('type', 'ouvrier');
    formData.append('nom', nom);
    formData.append('prenom', prenom);
    formData.append('cin', cin);
    formData.append('email', email);
    formData.append('pseudo', pseudo);
    formData.append('password', password);
    formData.append('description', description);
    formData.append('photo', photo);
    
    try {
        const response = await fetch('php/inscription.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('inscriptionModal')).hide();
            document.getElementById('formOuvrier').reset();
        } else {
            alert('Erreur : ' + data.message);
        }
    } catch (error) {
        alert('Erreur de connexion au serveur');
    }
});