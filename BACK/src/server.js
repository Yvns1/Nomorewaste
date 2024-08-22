const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const bcrypt = require('bcrypt');
const cors = require('cors');
const morgan = require('morgan');

const app = express();

// Middleware
app.use(morgan('dev'));  // 'dev' est un format de log prédéfini
app.use(bodyParser.json());
app.use(cors({
  origin: 'https://nomorewaste.fun',  // Modifiez cette ligne si vous utilisez localhost pour le développement
  methods: 'GET,POST,PUT,DELETE',
  allowedHeaders: 'Content-Type,Authorization'
}));

// Configuration de la base de données
const connection = mysql.createConnection({
  host: '127.0.0.1',
  user: 'root',
  password: 'R@ttr4pagesPA',
  database: 'WASTEFOOD'
});

connection.connect((err) => {
  if (err) {
    console.error('Erreur de connexion à la base de données:', err);
    return;
  }
  console.log('Connexion à la base de données réussie');
});

// Route d'inscription
app.post('/api/register', async (req, res) => {
  const { name, email, password, userType, additionalInfo } = req.body;

  try {
    // Hacher le mot de passe
    const hashedPassword = await bcrypt.hash(password, 10);

    // Vérifier si l'email existe déjà
    connection.query('SELECT * FROM Utilisateurs WHERE email = ?', [email], (err, results) => {
      if (err) {
        console.error('Erreur lors de la vérification de l\'email', err);
        return res.status(500).json({ success: false, message: 'Erreur de serveur' });
      }

      if (results.length > 0) {
        return res.status(400).json({ success: false, message: 'L\'email est déjà utilisé' });
      }

      // Insérer le nouvel utilisateur
      const newUser = { name, email, password_hash: hashedPassword, user_type: userType, additional_info: additionalInfo };
      connection.query('INSERT INTO Utilisateurs SET ?', newUser, (err) => {
        if (err) {
          console.error('Erreur lors de l\'insertion dans la base de données', err);
          return res.status(500).json({ success: false, message: 'Erreur lors de l\'inscription' });
        }

        res.status(200).json({ success: true, message: 'Inscription réussie' });
      });
    });
  } catch (err) {
    console.error('Erreur lors du traitement de l\'inscription', err);
    res.status(500).json({ success: false, message: 'Erreur lors de l\'inscription' });
  }
});

// Démarrer le serveur
const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`Serveur en écoute sur le port ${port}`);
});
