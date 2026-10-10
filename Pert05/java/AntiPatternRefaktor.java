package Pert05.java;

public class AntiPatternRefaktor {
    interface Bangun {
        double luas();
    }

    record LingkaranData(double r) implements Bangun {
        @Override
        public double luas(){
            return Math.PI * r * r;
        }
    }

    /**
     *  PersegiData
double sisi  implements Bangun        */
    record PersegiData(double sisi) implements Bangun {
        @Override 
        public double luas(){
            return sisi * sisi;
        }
    }

    record SegitigaData(double alas, double tinggi) implements Bangun{
        @Override 
        public double luas(){
            return 0.5 * alas * tinggi;
        }
    }

    public static void main(String [] args){
        Bangun[] daftar = {
            new LingkaranData(7),
            new PersegiData(5),
            new SegitigaData(2, 3)
        };

        double total = 0;
        for(Bangun b : daftar){
            total += b.luas();
        }

        System.out.printf("Total Luas (cara polimorfik) : %.2f%n", total);
    }

}
