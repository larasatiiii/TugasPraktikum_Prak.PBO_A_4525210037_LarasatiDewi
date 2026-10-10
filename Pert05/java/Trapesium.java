package Pert05.java;

public class Trapesium extends BangunDatar{
    private double sisiAtas;
    private double sisiBawah;
    private double tinggi;
    private double sisiKiri;
    private double sisiKanan;

    public Trapesium(double sisiAtas, double sisiBawah, double tinggi, double sisiKiri, double sisiKanan){
        super("Trapesium");
        this.sisiAtas = sisiAtas;
        this.sisiBawah = sisiBawah;
        this.tinggi = tinggi;
        this.sisiKiri = sisiKiri;
        this.sisiKanan = sisiKanan;
    }

    @Override 
    public double luas(){
        return 0.5 * (sisiAtas + sisiBawah) * tinggi;
    }

    @Override 
    public double keliling(){
        return sisiAtas + sisiBawah + sisiKiri + sisiKanan;
    }

    @Override 
    public String toString() {
        return getNama() + " (atas = " + sisiAtas +
        ", bawah = " + sisiBawah +
        ", tinggi = " + tinggi +
        ") luas = " + luas();
    }
}