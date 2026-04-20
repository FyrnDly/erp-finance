<?php

namespace App\Traits;

use Filament\Support\RawJs;

/**
 * @property-read string $heading
 * @property-read bool $isStack
 */
trait BarOptions {
    protected function getOptions(): RawJs  {
        $title = $this->heading;
        return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0,
                    title: {
                        display: true,
                        text: '{$title}'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 2
                            }).format(value);
                        }
                    }
                },
                x: {
                    display: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }

                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 2
                                }).format(context.parsed.y);
                            }

                            return label;
                        }
                    }
                }
            }
        }
        JS);
    }
}