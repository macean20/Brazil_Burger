package com.brasilburger;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.repositories.impl.BurgerRepository;
import com.brasilburger.repositories.impl.ComplementRepository;
import com.brasilburger.repositories.impl.MenuRepository;
import com.brasilburger.repositories.interfaces.IBurgerRepository;
import com.brasilburger.repositories.interfaces.IComplementRepository;
import com.brasilburger.repositories.interfaces.IMenuRepository;
import com.brasilburger.services.impl.BurgerService;
import com.brasilburger.services.impl.ComplementService;
import com.brasilburger.services.impl.MenuService;
import com.brasilburger.services.interfaces.IBurgerService;
import com.brasilburger.services.interfaces.IComplementService;
import com.brasilburger.services.interfaces.IMenuService;
import com.brasilburger.ui.MenuConsole;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class App {
    private static final Logger logger = LoggerFactory.getLogger(App.class);

    public static void main(String[] args) {
        logger.info("Démarrage de l'application Brasil Burger Console");

        try {
            IBurgerRepository burgerRepository = new BurgerRepository();
            IComplementRepository complementRepository = new ComplementRepository();
            IMenuRepository menuRepository = new MenuRepository();

            IBurgerService burgerService = new BurgerService(burgerRepository);
            IComplementService complementService = new ComplementService(complementRepository);
            IMenuService menuService = new MenuService(menuRepository);

            MenuConsole menuConsole = new MenuConsole(burgerService, complementService, menuService);

            System.out.println("\n════════════════════════════════════════");
            System.out.println("  BIENVENUE DANS BRASIL BURGER CONSOLE  ");
            System.out.println("    Système de Gestion des Ressources   ");
            System.out.println("════════════════════════════════════════\n");

            menuConsole.start();

        } catch (Exception e) {
            logger.error("Erreur fatale lors de l'exécution de l'application", e);
            System.err.println("\nErreur fatale: " + e.getMessage());
            System.err.println("Veuillez vérifier la configuration de la base de données dans application.properties");
        } finally {
            DatabaseConfig.closeDataSource();
            logger.info("Application terminée");
        }
    }
}
