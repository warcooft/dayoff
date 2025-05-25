<?php

namespace Aselsan\DayOff\Commands;

use DOMDocument;
use CodeIgniter\CLI\CLI;
use Aselsan\DayOff\Models\DayOffModel;
use CodeIgniter\CLI\BaseCommand;

class DayOff extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Scrap';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'scrap:dayoff';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Mengoleksi hari libur nasional yang bersumber dari web https://tanggalan.com';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'scrap:dayoff -year {year}';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        '--year' => 'Tahun yang akan menjadi scope pencarian, kosongkan jika ingin mencari tahun sekarang.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    protected string $SOURCE_URL = 'https://tanggalan.com/';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {

        $year = CLI::getOption('year');

        if ($year && strlen($year) !== 4) {
            return CLI::write("  Gagal : " . CLI::color("nilai parameter --year minimal 4 angka.", 'red'));
        }

        $year = $year ?? date('Y');

        $this->holiday($year);
    }

    protected function holiday($year)
    {
        $url_calendar = $this->SOURCE_URL . $year;


        $client = service('curlrequest');

        $response = $client->request('GET', $url_calendar);

        $body = $response->getBody();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Menyembunyikan pesan kesalahan parsial
        $dom->loadHTML($body);
        libxml_use_internal_errors(false); // Kembali menampilkan pesan kesalahan

        $articles = $dom->getElementsByTagName('article');

        $dataColllections = [];

        if ($articles) {
            // ambil elemen pertama dari array
            $article = $articles->item(0);

            $months = $article->getElementsByTagName('ul');

            $no = 1;
            foreach ($months as $key => $month) {
                $li = $month->getElementsByTagName('li');
                $holiday_container = $li->item($li->length - 1);

                $container_dayOffs = $holiday_container
                    ->getElementsByTagName('table');

                // jika node ditemukan, ambil node-nya
                if ($container_dayOffs->length > 0) {
                    $dayOffs = $container_dayOffs->item(0)
                        ->getElementsByTagName('tr');

                    foreach ($dayOffs as $key => $dayOff) {
                        $dataColllections[] = [
                            'date' => $dayOff->getElementsByTagName('td')->item(0)->nodeValue,
                            'month' => $no,
                            'year' => $year,
                            'title' => $dayOff->getElementsByTagName('td')->item(1)->nodeValue,
                        ];
                    }
                }

                $no++;
            }
        }

        foreach ($dataColllections as $key => $value) {
            $tanggal = trim($value['date']);

            if (str_contains($tanggal, '-')) {
                list($start, $end) = array_map('intval', explode('-', $tanggal));
                unset($dataColllections[$key]);

                foreach (range($start, $end) as $range) {
                    $dataColllections[] = [
                        'date' => $range,
                        'month' => $value['month'],
                        'year' => $value['year'],
                        'title' => $value['title'],
                    ];
                }
            } else {
                $dataColllections[$key]['date'] = (int)$tanggal;
            }
        }

        usort($dataColllections, function ($a, $b) {
            return ($a['month'] <=> $b['month']) ?: ($a['date'] <=> $b['date']);
        });

        $renewCollection = array_map(function ($item) {
            return [
                'date' => implode('/', [
                    $item['year'],
                    str_pad($item['month'], 2, '0', STR_PAD_LEFT),
                    str_pad($item['date'], 2, '0', STR_PAD_LEFT)
                ]),
                'title' => trim($item['title'])
            ];
        }, $dataColllections);

        $model = model('DayOffModel');
        foreach ($renewCollection as $key => $val) {
            if (!$model
                ->where('date', $val['date'])
                ->where('title', $val['title'])
                ->first()) {
                $model->insert($val);

                $renewCollection[$key]['status'] = 'inserted';
            } else {
                $renewCollection[$key]['status'] = 'skipped';
            }
        }

        CLI::write('    Total libur nasional tahun ' . $year . ' : ' . count($dataColllections) . ' hari');
        CLI::newLine();

        if ($renewCollection !== []) {
            CLI::table($renewCollection, ['date', 'title', 'status']);
            CLI::newLine();
        }
    }
}
