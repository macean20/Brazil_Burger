package com.brasilburger.ui;

import java.math.BigDecimal;
import java.util.Scanner;

public class InputHelper {
    private final Scanner scanner;

    public InputHelper(Scanner scanner) {
        this.scanner = scanner;
    }

    public String readString(String prompt) {
        System.out.print(prompt);
        return scanner.nextLine().trim();
    }

    public int readInt(String prompt) {
        while (true) {
            try {
                System.out.print(prompt);
                String input = scanner.nextLine().trim();
                return Integer.parseInt(input);
            } catch (NumberFormatException e) {
                System.out.println("Erreur: Veuillez entrer un nombre entier valide.");
            }
        }
    }

    public BigDecimal readBigDecimal(String prompt) {
        while (true) {
            try {
                System.out.print(prompt);
                String input = scanner.nextLine().trim();
                return new BigDecimal(input);
            } catch (NumberFormatException e) {
                System.out.println("Erreur: Veuillez entrer un nombre décimal valide (ex: 1500.50).");
            }
        }
    }

    public boolean readBoolean(String prompt) {
        while (true) {
            System.out.print(prompt + " (o/n): ");
            String input = scanner.nextLine().trim().toLowerCase();
            if (input.equals("o") || input.equals("oui")) {
                return true;
            } else if (input.equals("n") || input.equals("non")) {
                return false;
            } else {
                System.out.println("Erreur: Veuillez entrer 'o' pour oui ou 'n' pour non.");
            }
        }
    }

    public void pause() {
        System.out.println("\nAppuyez sur Entrée pour continuer...");
        scanner.nextLine();
    }
}
