const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const bcrypt = require('bcrypt');
const cors = require('cors');
const morgan = require('morgan');
require('dotenv').config(); // Pour utiliser les variables d'environnement

const app = express();

// Middleware
app.use(morgan('dev'));
app.use(bodyParser.json());
app.use(cors({
  origin: 'https://nomorewaste.fun',
  methods: 'GET,POST,PUT,DELETE',
  allowedHeaders: 'Content-Type,Authorization'
}));

// Route de test pour vérifier que le serveur est en ligne
app.get('/', (req, res) => {
  res.send('API Running');
});

// Configuration de la base de données
const connection = mysql.createConnection({
  host: process.env.DB_HOST || '127.0.0.1',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || 'R@ttr4pagesPA',
  database: process.env.DB_NAME || 'WASTEFOOD'
});

connection.connect((err) => {
  if (err) {
    console.error('Erreur de connexion à la base de données:', err);
    process.exit(1); // Arrêter le serveur si la connexion échoue
  }
  console.log('Connexion à la base de données réussie');
});

// Route d'inscription
app.post('/api/register', async (req, res) => {
  const { name, email, password, userType, additionalInfo } = req.body;

  try {
    // Vérifier si les champs obligatoires sont présents
    if (!name || !email || !password || !userType) {
      return res.status(400).json({ success: false, message: 'Tous les champs obligatoires doivent être remplis.' });
    }

    // Vérifier si l'email existe déjà
    connection.query('SELECT * FROM utilisateurs WHERE email = ?', [email], async (err, results) => {
      if (err) {
        console.error('Erreur lors de la vérification de l\'email:', err);
        return res.status(500).json({ success: false, message: 'Erreur de serveur' });
      }

      if (results.length > 0) {
        return res.status(400).json({ success: false, message: 'L\'email est déjà utilisé' });
      }

      // Hacher le mot de passe
      const hashedPassword = await bcrypt.hash(password, 10);

      console.log(req.body); // Ajouter ceci dans la route d'inscription pour voir ce qui est reçu

      // Insérer le nouvel utilisateur
      const newUser = {
        name,
        email,
        password_hash: hashedPassword,
        user_type: userType,
        additional_info: additionalInfo
      };

      connection.query('INSERT INTO utilisateurs SET ?', newUser, (err) => {
        if (err) {
          console.error('Erreur lors de l\'insertion dans la base de données:', err);
          return res.status(500).json({ success: false, message: 'Erreur lors de l\'inscription' });
        }

        console.log('Nouvel utilisateur ajouté avec succès:', newUser);
        res.status(200).json({ success: true, message: 'Inscription réussie' });
      });
    });
  } catch (err) {
    console.error('Erreur lors du traitement de l\'inscription:', err);
    res.status(500).json({ success: false, message: 'Erreur lors de l\'inscription' });
  }
});

// Démarrer le serveur
const port = process.env.PORT || 300;
app.listen(port, () => {
  console.log(`Serveur en écoute sur le port ${port}`);
});
