<?php

namespace App\Helpers;

use Carbon\Carbon;

class StatusHelper
{
    public static function getStatusAndValue($pr)
    {
        $po = $pr->pos ?? null;
        $progress = $po?->progress ?? null;
        $gr = $pr->gr ?? null;
        $payment = $po?->payment ?? null;

        $status = $pr->status_pekerjaan ?? 'PR';
        $waktu = '-';
        $nilai = 0;
        $label = 'PR';
        $class = 'text-secondary';

        switch ($status) {
            case 'PR':
                $waktu = $pr->tanggal_pr ? 'Tgl. PR: ' . Carbon::parse($pr->tanggal_pr)->format('Y-m-d') : '-';
                $nilai = $pr->nilai_pr ?? 0;
                $label = 'Purchase Request';
                $class = 'text-purple';
                break;

            case 'PO':
                $waktu = $po?->tanggal_po ? 'Tgl. PO: ' . Carbon::parse($po->tanggal_po)->format('Y-m-d') : '-';
                $nilai = $po->nilai_po ?? 0;
                $label = 'Purchase Order';
                $class = 'text-warning';
                break;

            case 'GR':
                $waktu = $gr?->tanggal_gr ? 'Tgl. GR: ' . Carbon::parse($gr->tanggal_gr)->format('Y-m-d') : '-';
                $nilai = $po->nilai_po ?? 0;
                $label = 'Realisasi (GR)';
                $class = 'text-info';
                break;

            case 'Payment':
                $waktu = $payment?->tanggal_payment ? 'Tgl. Payment: ' . Carbon::parse($payment->tanggal_payment)->format('Y-m-d') : '-';
                $nilai = $po->nilai_po ?? 0;
                $label = 'Payment';
                $class = 'text-success';
                break;
        }

        return [
            'status' => $status,
            'waktu' => $waktu,
            'nilai' => $nilai,
            'label' => $label,
            'class' => $class
        ];
    }
}