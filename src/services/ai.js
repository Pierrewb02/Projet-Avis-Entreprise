export function analyze(text) {

  text = text.toLowerCase();

  let sentiment = "neutral";
  let theme = "Général";

  /* =====================
     SENTIMENT
  ===================== */

  const positiveWords = [
    "bon",
    "super",
    "excellent",
    "rapide",
    "parfait",
    "top",
    "satisfait",
    "content"
  ];

  const negativeWords = [
    "mauvais",
    "nul",
    "lent",
    "horrible",
    "retard",
    "déçu",
    "decu",
    "cassé",
    "defectueux",
    "abimé",
    "abîmé",
    "cher",
    "problème",
    "probleme",
    "erreur",
    "bug"
  ];

  if (positiveWords.some(w => text.includes(w))) {
    sentiment = "positive";
  }

  if (negativeWords.some(w => text.includes(w))) {
    sentiment = "negative";
  }

  /* =====================
     THEMES
  ===================== */

  const themes = [

    {
      name: "Livraison",
      keywords: ["livraison", "retard", "transport", "colis"]
    },

    {
      name: "Qualité produit",
      keywords: [
        "produit",
        "qualité",
        "cassé",
        "defectueux",
        "abimé",
        "abîmé"
      ]
    },

    {
      name: "Prix",
      keywords: ["prix", "cher", "gratuit", "promotion"]
    },

    {
      name: "Service client",
      keywords: ["service", "support", "conseiller", "appel"]
    },

    {
      name: "Plateforme",
      keywords: ["site", "application", "bug", "erreur"]
    }

  ];

  for (const t of themes) {

    if (t.keywords.some(k => text.includes(k))) {
      theme = t.name;
      break;
    }

  }

  return {
    sentiment,
    theme
  };
}
