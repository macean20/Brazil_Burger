# Brasil Burger Console - Application Java

Application console Java pour la gestion des ressources du restaurant Brasil Burger (Burgers, Menus, Compléments).

## Technologies

- Java 11
- Maven 3.x
- PostgreSQL JDBC Driver
- HikariCP (Connection Pooling)
- SLF4J + Logback (Logging)

## Architecture SOLID

Le projet suit les principes SOLID:

### 1. Single Responsibility Principle (SRP)
- Chaque classe a une seule responsabilité
- Les modèles représentent uniquement les entités
- Les repositories gèrent uniquement l'accès aux données
- Les services contiennent uniquement la logique métier

### 2. Open/Closed Principle (OCP)
- Les classes sont ouvertes à l'extension mais fermées à la modification
- Utilisation d'interfaces pour les contrats

### 3. Liskov Substitution Principle (LSP)
- Les implémentations peuvent être substituées par leurs interfaces
- Toutes les implémentations respectent le contrat de leur interface

### 4. Interface Segregation Principle (ISP)
- Interfaces spécifiques et ciblées (IBurgerRepository, IComplementRepository, IMenuRepository)
- Pas d'interfaces "fourre-tout"

### 5. Dependency Inversion Principle (DIP)
- Les classes de haut niveau (Services) dépendent des abstractions (Interfaces)
- Les classes de bas niveau (Repositories) implémentent ces abstractions

## Structure du Projet

```
brasil-burger-console/
├── pom.xml
└── src/
    └── main/
        ├── java/
        │   └── com/
        │       └── brasilburger/
        │           ├── App.java                     # Point d'entrée
        │           ├── config/
        │           │   └── DatabaseConfig.java      # Configuration DB
        │           ├── models/
        │           │   ├── Burger.java
        │           │   ├── Complement.java
        │           │   ├── Menu.java
        │           │   └── TypeComplement.java
        │           ├── repositories/
        │           │   ├── interfaces/
        │           │   │   ├── IBurgerRepository.java
        │           │   │   ├── IComplementRepository.java
        │           │   │   └── IMenuRepository.java
        │           │   └── impl/
        │           │       ├── BurgerRepository.java
        │           │       ├── ComplementRepository.java
        │           │       └── MenuRepository.java
        │           ├── services/
        │           │   ├── interfaces/
        │           │   │   ├── IBurgerService.java
        │           │   │   ├── IComplementService.java
        │           │   │   └── IMenuService.java
        │           │   └── impl/
        │           │       ├── BurgerService.java
        │           │       ├── ComplementService.java
        │           │       └── MenuService.java
        │           └── ui/
        │               ├── InputHelper.java
        │               └── MenuConsole.java
        └── resources/
            └── application.properties               # Configuration
```

## Prérequis

1. **Java JDK 11 ou supérieur**
   ```bash
   java -version
   ```

2. **Maven 3.6 ou supérieur**
   ```bash
   mvn -version
   ```

3. **PostgreSQL 12 ou supérieur**
   - Base de données créée: `brasil_burger`
   - Schéma créé avec le fichier `schema.sql` à la racine du projet

## Configuration

1. Modifier le fichier `src/main/resources/application.properties`:
   ```properties
   db.url=jdbc:postgresql://localhost:5432/brasil_burger
   db.username=postgres
   db.password=votre_mot_de_passe
   db.driver=org.postgresql.Driver
   ```

2. Créer la base de données:
   ```bash
   psql -U postgres
   CREATE DATABASE brasil_burger;
   \c brasil_burger
   \i /chemin/vers/schema.sql
   ```

## Compilation et Exécution

### Compiler le projet
```bash
cd brasil-burger-console
mvn clean compile
```

### Créer le JAR exécutable
```bash
mvn clean package
```

Cela créera un fichier JAR dans `target/brasil-burger-console-1.0.0-jar-with-dependencies.jar`

### Exécuter l'application

**Option 1: Avec Maven**
```bash
mvn exec:java -Dexec.mainClass="com.brasilburger.App"
```

**Option 2: Avec le JAR**
```bash
java -jar target/brasil-burger-console-1.0.0-jar-with-dependencies.jar
```

**Option 3: Directement avec Java (après compilation)**
```bash
cd target/classes
java com.brasilburger.App
```

## Fonctionnalités

### Gestion des Burgers
- Créer un nouveau burger
- Afficher tous les burgers
- Afficher uniquement les burgers disponibles
- Modifier un burger existant
- Supprimer un burger
- Changer la disponibilité d'un burger

### Gestion des Compléments
- Créer un nouveau complément (BOISSON ou FRITE)
- Afficher tous les compléments
- Filtrer les compléments par type
- Modifier un complément
- Supprimer un complément

### Gestion des Menus
- Créer un nouveau menu (Burger + Boisson + Frites)
- Afficher tous les menus
- Afficher les menus avec leurs détails complets
- Modifier un menu
- Supprimer un menu

## Tests

Pour exécuter les tests (si vous en ajoutez):
```bash
mvn test
```

## Logs

Les logs sont configurés avec Logback et s'affichent dans la console.
Vous pouvez modifier le niveau de log dans `src/main/resources/logback.xml` (à créer si nécessaire).

## Dépannage

### Erreur de connexion à la base de données
- Vérifiez que PostgreSQL est démarré
- Vérifiez les credentials dans `application.properties`
- Vérifiez que la base de données `brasil_burger` existe
- Vérifiez que le schéma a été créé avec `schema.sql`

### Erreur de compilation Maven
```bash
mvn clean install -U
```

### Port PostgreSQL occupé
Par défaut, PostgreSQL utilise le port 5432. Si vous utilisez un port différent, modifiez l'URL dans `application.properties`.

## Licence

Ce projet est destiné à un usage éducatif dans le cadre du cours.
