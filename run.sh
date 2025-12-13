#!/bin/bash

# Script de lancement rapide de l'application Brasil Burger Console

echo "╔════════════════════════════════════════╗"
echo "║   BRASIL BURGER - DÉMARRAGE            ║"
echo "╚════════════════════════════════════════╝"
echo ""

# Vérifier si Maven est installé
if ! command -v mvn &> /dev/null; then
    echo "❌ Maven n'est pas installé. Veuillez l'installer d'abord."
    echo "   Ubuntu/Debian: sudo apt install maven"
    echo "   Fedora: sudo dnf install maven"
    echo "   Arch: sudo pacman -S maven"
    exit 1
fi

# Vérifier si Java est installé
if ! command -v java &> /dev/null; then
    echo "❌ Java n'est pas installé. Veuillez installer Java JDK 11 ou supérieur."
    echo "   Ubuntu/Debian: sudo apt install openjdk-11-jdk"
    echo "   Fedora: sudo dnf install java-11-openjdk-devel"
    echo "   Arch: sudo pacman -S jdk11-openjdk"
    exit 1
fi

echo "✓ Java installé: $(java -version 2>&1 | head -n 1)"
echo "✓ Maven installé: $(mvn -version | head -n 1)"
echo ""

# Compiler le projet si nécessaire
if [ ! -f "target/brasil-burger-console-1.0.0-jar-with-dependencies.jar" ]; then
    echo "📦 Compilation du projet..."
    mvn clean package -q
    if [ $? -ne 0 ]; then
        echo "❌ Erreur lors de la compilation"
        exit 1
    fi
    echo "✓ Compilation réussie"
    echo ""
fi

# Lancer l'application
echo "🚀 Lancement de l'application..."
echo ""
java -jar target/brasil-burger-console-1.0.0-jar-with-dependencies.jar
