<?php

namespace Zarbinco\PersianCore\Data;

final class IranianBanks
{
    /** @var array<int, array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>}> */
    private const BANKS = [
        [
            'slug' => 'melli',
            'name' => 'Bank Melli Iran',
            'name_fa' => 'بانک ملی ایران',
            'card_bins' => ['603799'],
            'sheba_codes' => ['017'],
        ],
        [
            'slug' => 'sepah',
            'name' => 'Bank Sepah',
            'name_fa' => 'بانک سپه',
            'card_bins' => ['589210'],
            'sheba_codes' => ['015'],
        ],
        [
            'slug' => 'mellat',
            'name' => 'Bank Mellat',
            'name_fa' => 'بانک ملت',
            'card_bins' => ['610433', '991975'],
            'sheba_codes' => ['012'],
        ],
        [
            'slug' => 'tejarat',
            'name' => 'Bank Tejarat',
            'name_fa' => 'بانک تجارت',
            'card_bins' => ['627353', '585983'],
            'sheba_codes' => ['018'],
        ],
        [
            'slug' => 'saderat',
            'name' => 'Bank Saderat Iran',
            'name_fa' => 'بانک صادرات ایران',
            'card_bins' => ['603769'],
            'sheba_codes' => ['019'],
        ],
        [
            'slug' => 'keshavarzi',
            'name' => 'Bank Keshavarzi',
            'name_fa' => 'بانک کشاورزی',
            'card_bins' => ['603770', '639217'],
            'sheba_codes' => ['016'],
        ],
        [
            'slug' => 'maskan',
            'name' => 'Bank Maskan',
            'name_fa' => 'بانک مسکن',
            'card_bins' => ['628023'],
            'sheba_codes' => ['014'],
        ],
        [
            'slug' => 'saman',
            'name' => 'Saman Bank',
            'name_fa' => 'بانک سامان',
            'card_bins' => ['621986'],
            'sheba_codes' => ['056'],
        ],
        [
            'slug' => 'pasargad',
            'name' => 'Bank Pasargad',
            'name_fa' => 'بانک پاسارگاد',
            'card_bins' => ['639347', '502229'],
            'sheba_codes' => ['057'],
        ],
        [
            'slug' => 'parsian',
            'name' => 'Parsian Bank',
            'name_fa' => 'بانک پارسیان',
            'card_bins' => ['622106', '639194', '627884'],
            'sheba_codes' => ['054'],
        ],
        [
            'slug' => 'sina',
            'name' => 'Sina Bank',
            'name_fa' => 'بانک سینا',
            'card_bins' => ['639346'],
            'sheba_codes' => ['059'],
        ],
        [
            'slug' => 'sarmayeh',
            'name' => 'Sarmayeh Bank',
            'name_fa' => 'بانک سرمایه',
            'card_bins' => ['639607'],
            'sheba_codes' => ['058'],
        ],
        [
            'slug' => 'shahr',
            'name' => 'Shahr Bank',
            'name_fa' => 'بانک شهر',
            'card_bins' => ['502806', '504706'],
            'sheba_codes' => ['061'],
        ],
        [
            'slug' => 'ayandeh',
            'name' => 'Ayandeh Bank',
            'name_fa' => 'بانک آینده',
            'card_bins' => ['636214'],
            'sheba_codes' => ['062'],
        ],
        [
            'slug' => 'karafarin',
            'name' => 'Karafarin Bank',
            'name_fa' => 'بانک کارآفرین',
            'card_bins' => ['627488', '502910'],
            'sheba_codes' => ['053'],
        ],
        [
            'slug' => 'eghtesad_novin',
            'name' => 'Eghtesad Novin Bank',
            'name_fa' => 'بانک اقتصاد نوین',
            'card_bins' => ['627412'],
            'sheba_codes' => ['055'],
        ],
        [
            'slug' => 'post',
            'name' => 'Post Bank Iran',
            'name_fa' => 'پست بانک ایران',
            'card_bins' => ['627760'],
            'sheba_codes' => ['021'],
        ],
        [
            'slug' => 'tosee_saderat',
            'name' => 'Export Development Bank of Iran',
            'name_fa' => 'بانک توسعه صادرات ایران',
            'card_bins' => ['627648', '207177'],
            'sheba_codes' => ['020'],
        ],
        [
            'slug' => 'tosee_taavon',
            'name' => 'Tosee Taavon Bank',
            'name_fa' => 'بانک توسعه تعاون',
            'card_bins' => ['502908'],
            'sheba_codes' => ['022'],
        ],
        [
            'slug' => 'refah',
            'name' => 'Refah Bank',
            'name_fa' => 'بانک رفاه کارگران',
            'card_bins' => ['589463'],
            'sheba_codes' => ['013'],
        ],
        [
            'slug' => 'day',
            'name' => 'Day Bank',
            'name_fa' => 'بانک دی',
            'card_bins' => ['502938'],
            'sheba_codes' => ['066'],
        ],
        [
            'slug' => 'iran_zamin',
            'name' => 'Iran Zamin Bank',
            'name_fa' => 'بانک ایران زمین',
            'card_bins' => ['505785'],
            'sheba_codes' => ['069'],
        ],
        [
            'slug' => 'resalat',
            'name' => 'Resalat Bank',
            'name_fa' => 'بانک قرض الحسنه رسالت',
            'card_bins' => ['504172'],
            'sheba_codes' => ['070'],
        ],
        [
            'slug' => 'middle_east',
            'name' => 'Middle East Bank',
            'name_fa' => 'بانک خاورمیانه',
            'card_bins' => ['585947'],
            'sheba_codes' => ['078'],
        ],
        [
            'slug' => 'mehr_iran',
            'name' => 'Mehr Iran Bank',
            'name_fa' => 'بانک قرض الحسنه مهر ایران',
            'card_bins' => ['606373'],
            'sheba_codes' => ['060', '090'],
        ],
    ];

    /** @return array<int, array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>}> */
    public static function all(): array
    {
        return self::BANKS;
    }

    /** @return array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>}|null */
    public static function bySlug(string $slug): ?array
    {
        foreach (self::BANKS as $bank) {
            if ($bank['slug'] === $slug) {
                return $bank;
            }
        }

        return null;
    }

    public static function slugByCardBin(string $bin): ?string
    {
        $matchedSlug = null;
        $matchedLength = 0;

        foreach (self::BANKS as $bank) {
            foreach ($bank['card_bins'] as $candidate) {
                $length = strlen($candidate);

                if ($length > $matchedLength && str_starts_with($bin, $candidate)) {
                    $matchedSlug = $bank['slug'];
                    $matchedLength = $length;
                }
            }
        }

        return $matchedSlug;
    }

    public static function slugByShebaCode(string $code): ?string
    {
        foreach (self::BANKS as $bank) {
            if (in_array($code, $bank['sheba_codes'], true)) {
                return $bank['slug'];
            }
        }

        return null;
    }
}
